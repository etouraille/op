import { createSelector } from '@ngrx/store';

export interface FeatureState {
  logged: boolean;
}

export interface AppState {
  login: FeatureState;
}

export const selectFeature = (state: AppState) => state.login;

export const selectFeatureLogged = createSelector(
  selectFeature,
  (state: FeatureState) => state.logged
);
