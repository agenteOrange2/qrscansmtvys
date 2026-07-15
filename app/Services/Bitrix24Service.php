<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Cliente REST de Bitrix24 vía webhook entrante
 * (https://smtvys.bitrix24.com/rest/{usuario}/{token}/).
 * Mismo patrón que el sitio WordPress stmvys (kuiraweb-theme).
 */
class Bitrix24Service
{
    public function __construct(private readonly string $webhook) {}

    public static function fromSettings(): self
    {
        return new self((string) Setting::get('bitrix.webhook', ''));
    }

    public static function isEnabled(): bool
    {
        return Setting::getBool('bitrix.enabled')
            && trim((string) Setting::get('bitrix.webhook', '')) !== '';
    }

    /**
     * Llamada genérica a la REST API. Devuelve el payload `result`
     * o lanza RuntimeException con el mensaje de error.
     */
    public function call(string $method, array $params = []): mixed
    {
        if (trim($this->webhook) === '') {
            throw new RuntimeException('No hay webhook de Bitrix24 configurado.');
        }

        try {
            $response = Http::timeout(15)
                ->asJson()
                ->post(rtrim($this->webhook, '/')."/{$method}.json", $params);
        } catch (ConnectionException $e) {
            throw new RuntimeException("No se pudo conectar con Bitrix24: {$e->getMessage()}");
        }

        $body = $response->json();

        if (is_array($body) && ! empty($body['error'])) {
            throw new RuntimeException((string) ($body['error_description'] ?? $body['error']));
        }

        if ($response->failed()) {
            throw new RuntimeException("Bitrix24 respondió HTTP {$response->status()} en {$method}.");
        }

        return is_array($body) ? ($body['result'] ?? null) : null;
    }

    /**
     * Perfil del usuario dueño del webhook — se usa para probar la conexión.
     *
     * @return array<string, mixed>
     */
    public function profile(): array
    {
        $result = $this->call('profile');

        if (! is_array($result)) {
            throw new RuntimeException('Respuesta inesperada de Bitrix24 al probar la conexión.');
        }

        return $result;
    }

    /**
     * Pipelines (categorías) de deals, normalizados a [{id, name}].
     *
     * @return list<array{id: int, name: string}>
     */
    public function dealCategories(): array
    {
        try {
            $result = $this->call('crm.category.list', ['entityTypeId' => 2]);
            $categories = collect($result['categories'] ?? [])
                ->map(fn (array $category) => [
                    'id' => (int) $category['id'],
                    'name' => (string) $category['name'],
                ]);
        } catch (RuntimeException) {
            // Portales sin el método nuevo: API clásica de categorías de deals
            $result = $this->call('crm.dealcategory.list');
            $categories = collect(is_array($result) ? $result : [])
                ->map(fn (array $category) => [
                    'id' => (int) $category['ID'],
                    'name' => (string) $category['NAME'],
                ]);
        }

        if (! $categories->contains(fn (array $category) => $category['id'] === 0)) {
            $categories->prepend(['id' => 0, 'name' => 'General']);
        }

        return $categories->sortBy('id')->values()->all();
    }

    /**
     * Etapas de un pipeline, normalizadas a [{id, name}].
     *
     * @return list<array{id: string, name: string}>
     */
    public function dealStages(int $categoryId): array
    {
        $result = $this->call('crm.dealcategory.stage.list', ['id' => $categoryId]);

        return collect(is_array($result) ? $result : [])
            ->map(fn (array $stage) => [
                'id' => (string) $stage['STATUS_ID'],
                'name' => (string) $stage['NAME'],
            ])
            ->values()
            ->all();
    }

    /**
     * Busca un contacto por correo/teléfono (dedupe) o lo crea.
     *
     * @param  array{name?: ?string, last_name?: ?string, email?: ?string, phone?: ?string, company?: ?string}  $contact
     */
    public function findOrCreateContact(array $contact): int
    {
        foreach ([['EMAIL', $contact['email'] ?? ''], ['PHONE', $contact['phone'] ?? '']] as [$type, $value]) {
            if (trim((string) $value) === '') {
                continue;
            }

            $found = $this->call('crm.duplicate.findbycomm', [
                'entity_type' => 'CONTACT',
                'type' => $type,
                'values' => [$value],
            ]);

            if (! empty($found['CONTACT'][0])) {
                return (int) $found['CONTACT'][0];
            }
        }

        $fields = array_filter([
            'NAME' => $contact['name'] ?? '',
            'LAST_NAME' => $contact['last_name'] ?? '',
            'COMPANY_TITLE' => $contact['company'] ?? '',
            'EMAIL' => ! empty($contact['email']) ? [['VALUE' => $contact['email'], 'VALUE_TYPE' => 'WORK']] : null,
            'PHONE' => ! empty($contact['phone']) ? [['VALUE' => $contact['phone'], 'VALUE_TYPE' => 'WORK']] : null,
            'SOURCE_ID' => 'WEB',
        ]);

        $id = (int) $this->call('crm.contact.add', ['fields' => $fields]);

        if ($id <= 0) {
            throw new RuntimeException('No se pudo crear el contacto en Bitrix24.');
        }

        return $id;
    }

    /**
     * Crea un deal y devuelve su ID.
     *
     * @param  array<string, mixed>  $fields
     */
    public function addDeal(array $fields): int
    {
        $id = (int) $this->call('crm.deal.add', ['fields' => $fields]);

        if ($id <= 0) {
            throw new RuntimeException('No se pudo crear el deal en Bitrix24.');
        }

        return $id;
    }
}
