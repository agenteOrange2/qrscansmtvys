export interface ScannedContact {
    nombre: string;
    apellidos: string;
    puesto: string;
    empresa: string;
    estado: string;
    telefono: string;
    rol: string;
    correo: string;
    campos_adicionales: string[];
}

export function emptyContact(): ScannedContact {
    return {
        nombre: '',
        apellidos: '',
        puesto: '',
        empresa: '',
        estado: '',
        telefono: '',
        rol: '',
        correo: '',
        campos_adicionales: [],
    };
}

/**
 * Parsea el contenido de un gafete QR.
 *
 * Formato principal (heredado del sistema original): cadena delimitada por
 * `^`, `*`, `-` o `|`, descartando el primer segmento y mapeando por posición:
 * `^Nombre^Apellidos^Puesto^Empresa^Telefono^Rol^Correo^extra1^extra2...`
 *
 * Adicionalmente soporta vCard (BEGIN:VCARD) y texto plano como respaldo.
 */
export function parseQrData(decodedText: string): ScannedContact {
    const text = decodedText.trim();

    if (/^BEGIN:VCARD/im.test(text)) {
        return parseVCard(text);
    }

    const delimiters = ['^', '*', '|', '-'];
    const delimiter = delimiters.find((d) => text.includes(d));

    if (delimiter) {
        const values = text.split(delimiter).slice(1).map((v) => v.trim());

        return {
            nombre: values[0] ?? '',
            apellidos: values[1] ?? '',
            puesto: values[2] ?? '',
            empresa: values[3] ?? '',
            estado: '',
            telefono: values[4] ?? '',
            rol: values[5] ?? '',
            correo: values[6] ?? '',
            campos_adicionales: values.slice(7).filter((v) => v !== ''),
        };
    }

    return { ...emptyContact(), nombre: text };
}

function vCardValue(text: string, field: string): string {
    const match = text.match(new RegExp(`^${field}[^:]*:(.+)$`, 'im'));

    return match ? match[1].trim() : '';
}

function parseVCard(text: string): ScannedContact {
    const n = vCardValue(text, 'N').split(';');
    const fn = vCardValue(text, 'FN');

    let nombre = n[1] ?? '';
    let apellidos = n[0] ?? '';

    if (!nombre && fn) {
        const parts = fn.split(/\s+/);
        nombre = parts[0] ?? '';
        apellidos = parts.slice(1).join(' ');
    }

    return {
        nombre,
        apellidos,
        puesto: vCardValue(text, 'TITLE'),
        empresa: vCardValue(text, 'ORG').split(';')[0] ?? '',
        estado: '',
        telefono: vCardValue(text, 'TEL'),
        rol: '',
        correo: vCardValue(text, 'EMAIL'),
        campos_adicionales: [],
    };
}
