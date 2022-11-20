import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {HomeComponent} from "./home/home.component";
import {LoginComponent} from "./login/login.component";
import {SubscribeComponent} from "./subscribe/subscribe.component";
import {ThingsComponent} from "./things/things.component";

const routes: Routes = [{
  path: '',
  component: HomeComponent,
  children: [
    { path: 'login', component: LoginComponent},
    { path: 'subscribe', component: SubscribeComponent},
    { path: '', component: ThingsComponent}
  ]
}];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
