import _debounce from "debounce";

export const debounce = _debounce;

export function useDebouncedRef(val, delay, immediate) {
	const state = ref(val);
	return customRef((track, trigger) => ({
		get() {
			track();
			return state.value;
		},
		set: debounce(value => {
			state.value = value;
			trigger();
		}, delay, immediate),
	}));
}

export default useDebouncedRef;