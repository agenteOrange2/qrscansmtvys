import Toastify from 'toastify-js';
import 'toastify-js/src/toastify.css';

type NotifyType = 'success' | 'error' | 'warning' | 'info';

const colors: Record<NotifyType, string> = {
    success: 'linear-gradient(135deg, #16a34a, #15803d)',
    error: 'linear-gradient(135deg, #dc2626, #b91c1c)',
    warning: 'linear-gradient(135deg, #d97706, #b45309)',
    info: 'linear-gradient(135deg, #2563eb, #1d4ed8)',
};

export function notify(message: string, type: NotifyType = 'success', duration = 4000): void {
    Toastify({
        text: message,
        duration,
        gravity: 'top',
        position: 'right',
        close: true,
        stopOnFocus: true,
        style: {
            background: colors[type],
            borderRadius: '0.75rem',
            boxShadow: '0 10px 25px -5px rgb(0 0 0 / 0.25)',
            fontFamily: 'inherit',
        },
    }).showToast();
}
