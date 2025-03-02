import _numeral from 'numeral';
const toUrl = window.toUrl;

export function currency(value, format = '0,0') {
    return _numeral(value).format(format);
}

export function remoteFile(v, format){
    const REGEX = /^[a-z0-9]{1,16}$/;
            if (REGEX.test(v)) {
                return toUrl('file/view', { id: v }) + (format ? '/' + format : '');
            }
            return v;
}

export const numeral = _numeral;
