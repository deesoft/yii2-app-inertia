import _numeral from 'numeral';
import URL from './url';

export function currency(value, format = '0,0') {
    return _numeral(value).format(format);
}

export function remoteFile(v, format){
    return URL.remote(v,format);
}

export const numeral = _numeral;
