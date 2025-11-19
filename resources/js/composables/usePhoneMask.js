import { onMounted, onUnmounted } from 'vue';
import IMask from 'imask';

export function usePhoneMask(inputRef) {
    let maskInstance = null;

    const initMask = () => {
        if (inputRef.value) {
            maskInstance = IMask(inputRef.value, {
                mask: '+{7} (000) 000-00-00',
                lazy: false,
                placeholderChar: '_'
            });
        }
    };

    const destroyMask = () => {
        if (maskInstance) {
            maskInstance.destroy();
            maskInstance = null;
        }
    };

    onMounted(() => {
        initMask();
    });

    onUnmounted(() => {
        destroyMask();
    });

    return {
        maskInstance,
        initMask,
        destroyMask
    };
}
