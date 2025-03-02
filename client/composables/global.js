const STORAGE_KEY = '__theme';
const theme = ref(localStorage.getItem(STORAGE_KEY));

export const darkMode = computed({
    get() {
        return theme.value == 'dark';
    },
    set(value) {
        theme.value = value ? 'dark' : 'light';
        localStorage.setItem(STORAGE_KEY, theme.value);
    }
});
const dialogMethods = reactive({
    confirm: null,
    uploadImage: null,
    uploadFile: null,
});

export function applyDialog(name, method) {
    switch (name) {
        case 'confirm':
            dialogMethods.confirm = method;
            break;
        case 'uploadImage':
            dialogMethods.uploadImage = method;
            break;
            case 'uploadFile':
                dialogMethods.uploadFile = method;
                break;
    }
}

export function confirm(message, callbackTrue, callbackFalse) {
    if (dialogMethods.confirm) {
        return dialogMethods.confirm(message, callbackTrue, callbackFalse);
    }

    return new Promise((resolve, reject) => {
        if (window.confirm(message)) {
            if (callbackTrue) {
                callbackTrue();
            }
            resolve(true);
        } else {
            if (callbackFalse) {
                callbackFalse();
            }
            reject(false);
        }
    });
}

export function uploadImage(options) {
    if (dialogMethods.uploadImage) {
        return dialogMethods.uploadImage(options);
    }
    return new Promise((resolve) => {
        resolve(false);
    });
}

export function uploadFile(options) {
    if (dialogMethods.uploadFile) {
        return dialogMethods.uploadFile(options);
    }
    return new Promise((resolve) => {
        resolve(false);
    });
}