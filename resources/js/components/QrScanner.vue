<script setup lang="ts">
import Lucide from '@/components/Base/Lucide';
import { FormSelect } from '@/components/Base/Form';
import { onBeforeUnmount, ref } from 'vue';
import { QrcodeStream, type DetectedBarcode } from 'vue-qrcode-reader';

const props = withDefaults(
    defineProps<{
        paused?: boolean;
        /** milisegundos a ignorar re-lecturas del mismo código */
        cooldown?: number;
    }>(),
    { paused: false, cooldown: 1500 },
);

const emit = defineEmits<{
    (e: 'detect', rawValue: string): void;
}>();

const cameraReady = ref(false);
const errorMessage = ref('');
const torchActive = ref(false);
const torchSupported = ref(false);
const cameras = ref<MediaDeviceInfo[]>([]);
const selectedCameraId = ref('');
const flash = ref(false);

let lastValue = '';
let lastTime = 0;
let audioCtx: AudioContext | null = null;

const constraints = ref<MediaTrackConstraints>({ facingMode: 'environment' });

function beep(): void {
    try {
        audioCtx = audioCtx ?? new AudioContext();
        const oscillator = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        oscillator.type = 'sine';
        oscillator.frequency.value = 880;
        gain.gain.setValueAtTime(0.15, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.18);
        oscillator.connect(gain).connect(audioCtx.destination);
        oscillator.start();
        oscillator.stop(audioCtx.currentTime + 0.2);
    } catch {
        // audio no disponible: ignorar
    }
}

function onDetect(codes: DetectedBarcode[]): void {
    const raw = codes[0]?.rawValue?.trim();

    if (!raw) return;

    const now = Date.now();

    if (raw === lastValue && now - lastTime < props.cooldown) return;

    lastValue = raw;
    lastTime = now;

    beep();
    navigator.vibrate?.(120);
    flash.value = true;
    setTimeout(() => (flash.value = false), 350);

    emit('detect', raw);
}

async function onCameraOn(capabilities: MediaTrackCapabilities): Promise<void> {
    cameraReady.value = true;
    errorMessage.value = '';
    torchSupported.value = 'torch' in capabilities;

    try {
        const devices = await navigator.mediaDevices.enumerateDevices();
        cameras.value = devices.filter((device) => device.kind === 'videoinput');
    } catch {
        cameras.value = [];
    }
}

function onError(error: Error): void {
    cameraReady.value = false;

    const messages: Record<string, string> = {
        NotAllowedError: 'Debes otorgar permiso para acceder a la cámara.',
        NotFoundError: 'No se encontró ninguna cámara en este dispositivo.',
        NotReadableError: 'La cámara está siendo usada por otra aplicación.',
        OverconstrainedError: 'La cámara seleccionada no es compatible.',
        InsecureContextError: 'El escáner requiere HTTPS (o localhost) para acceder a la cámara.',
    };

    errorMessage.value = messages[error.name] ?? `Error de cámara: ${error.message}`;
}

function switchCamera(): void {
    constraints.value = selectedCameraId.value
        ? { deviceId: { exact: selectedCameraId.value } }
        : { facingMode: 'environment' };
}

function paintTracker(codes: DetectedBarcode[], ctx: CanvasRenderingContext2D): void {
    for (const code of codes) {
        const [first, ...rest] = code.cornerPoints;
        ctx.strokeStyle = '#22c55e';
        ctx.lineWidth = 4;
        ctx.lineJoin = 'round';
        ctx.beginPath();
        ctx.moveTo(first.x, first.y);
        for (const { x, y } of rest) ctx.lineTo(x, y);
        ctx.closePath();
        ctx.stroke();
    }
}

onBeforeUnmount(() => {
    audioCtx?.close();
});
</script>

<template>
    <div class="space-y-3">
        <div
            class="relative mx-auto w-full max-w-md overflow-hidden rounded-2xl bg-slate-900 shadow-lg aspect-square"
        >
            <QrcodeStream
                :constraints="constraints"
                :formats="['qr_code']"
                :paused="paused"
                :torch="torchActive"
                :track="paintTracker"
                class="h-full w-full"
                @detect="onDetect"
                @camera-on="onCameraOn"
                @error="onError"
            >
                <!-- Marco animado de escaneo -->
                <div v-if="cameraReady && !paused" class="pointer-events-none absolute inset-0">
                    <div class="absolute inset-[12%]">
                        <span class="absolute left-0 top-0 h-10 w-10 rounded-tl-xl border-l-4 border-t-4 border-white/90"></span>
                        <span class="absolute right-0 top-0 h-10 w-10 rounded-tr-xl border-r-4 border-t-4 border-white/90"></span>
                        <span class="absolute bottom-0 left-0 h-10 w-10 rounded-bl-xl border-b-4 border-l-4 border-white/90"></span>
                        <span class="absolute bottom-0 right-0 h-10 w-10 rounded-br-xl border-b-4 border-r-4 border-white/90"></span>
                        <span class="scanner-line absolute inset-x-2 top-0 h-0.5 rounded bg-success shadow-[0_0_12px_2px_rgba(34,197,94,0.8)]"></span>
                    </div>
                </div>

                <!-- Flash de éxito -->
                <Transition
                    enter-active-class="transition-opacity duration-100"
                    leave-active-class="transition-opacity duration-300"
                    enter-from-class="opacity-0"
                    leave-to-class="opacity-0"
                >
                    <div v-if="flash" class="absolute inset-0 bg-success/40"></div>
                </Transition>

                <!-- Pausado -->
                <div
                    v-if="paused"
                    class="absolute inset-0 flex flex-col items-center justify-center gap-2 bg-slate-900/70 text-white backdrop-blur-sm"
                >
                    <Lucide icon="PauseCircle" class="h-12 w-12" />
                    <span class="text-sm">Escáner en pausa</span>
                </div>

                <!-- Error -->
                <div
                    v-if="errorMessage"
                    class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-slate-900/90 p-6 text-center text-white"
                >
                    <Lucide icon="CameraOff" class="h-12 w-12 text-danger" />
                    <p class="text-sm">{{ errorMessage }}</p>
                </div>
            </QrcodeStream>
        </div>

        <div class="mx-auto flex w-full max-w-md items-center gap-2">
            <FormSelect
                v-if="cameras.length > 1"
                v-model="selectedCameraId"
                class="flex-1"
                formSelectSize="sm"
                @change="switchCamera"
            >
                <option value="">Cámara trasera (auto)</option>
                <option v-for="camera in cameras" :key="camera.deviceId" :value="camera.deviceId">
                    {{ camera.label || `Cámara ${camera.deviceId.slice(0, 6)}` }}
                </option>
            </FormSelect>
            <button
                v-if="torchSupported"
                type="button"
                class="ml-auto flex items-center gap-1.5 rounded-md border border-slate-200 px-3 py-1.5 text-xs shadow-sm transition hover:bg-slate-50 dark:border-darkmode-400 dark:hover:bg-darkmode-400"
                :class="{ 'bg-warning/20 border-warning text-warning': torchActive }"
                @click="torchActive = !torchActive"
            >
                <Lucide :icon="torchActive ? 'FlashlightOff' : 'Flashlight'" class="h-4 w-4" />
                Linterna
            </button>
        </div>
    </div>
</template>

<style scoped>
.scanner-line {
    animation: scan-sweep 2.2s ease-in-out infinite;
}

@keyframes scan-sweep {
    0%,
    100% {
        top: 4%;
    }
    50% {
        top: 94%;
    }
}
</style>
