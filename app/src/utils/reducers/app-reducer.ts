import {Action, createReducer, on } from "@ngrx/store";
import * as loginAction from '../actions/login-action';

export interface State {
  logged: boolean;
}

export const initialState: State = {
  logged: false
}

export const loginReducer = createReducer(
  initialState,
  on(loginAction.login,state => ({ ...state, logged: true })),
  on(loginAction.logout,state => ({ ...state, logged: false }))
)
