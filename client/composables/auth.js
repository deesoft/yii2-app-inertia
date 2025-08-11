const user = window.user || {};
const { yiiUrl } = window;
export const auth = reactive({
    ...user,
    isLoged: computed(() => !!user.id),
    avatarLink: computed(() => {
        if (auth.avatar) {
            const regex = /^[a-z0-9]{1,16}$/;
            return regex.test(auth.avatar) ? yiiUrl('/file/view', { id: auth.avatar }) : auth.avatar;
        } else {
            return yiiUrl.public('icon/avatar.png');
        }
    }),
});