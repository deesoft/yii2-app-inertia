const user = window.user || {};
const { toUrl } = window;
export const auth = reactive({
    ...user,
    isLoged: computed(() => !!user.id),
    avatarLink: computed(() => {
        if (auth.avatar) {
            const regex = /^[a-z0-9]{1,16}$/;
            return regex.test(auth.avatar) ? toUrl('/file/view', { id: auth.avatar }) : auth.avatar;
        } else {
            return toUrl.public('icon/avatar.png');
        }
    }),
});