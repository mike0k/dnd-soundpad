import { store } from '../store/Store';
import * as LayoutAction from '../store/action/LayoutAction';

//const clear = () => store.dispatch(LayoutAction.clear());
export const get = () => store.getState().layout;
export const set = (data) => store.dispatch(LayoutAction.set(data));
//const update = (data) => store.dispatch(LayoutAction.merge(data));
