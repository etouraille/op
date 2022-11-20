import { Component, OnInit } from '@angular/core';
import {Store} from "@ngrx/store";
import {selectFeatureLogged} from "../../selectors/logged-selector";
import {SubscribeComponent} from "../subscribe/subscribe.component";
import {HttpClient, HttpClientModule} from "@angular/common/http";
import {login, logout} from "../../actions/login-action";

@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.scss']
})
export class HeaderComponent extends SubscribeComponent implements OnInit {

  logged: boolean = true;
  email: string = '';

  constructor(
    private store: Store<{logged: boolean}>,
    private http: HttpClient,
  ) {
    super();
  }

  ngOnInit(): void {
    this.add(this.store.select((state:any) => state.login.logged).subscribe(data => {
        this.logged = data;
    }));
    this.add(
      this.http.get('api/ping').subscribe((data:any) => {
        if(data.email) {
          this.email = data.email;
          this.store.dispatch(login());
        } else {
          this.store.dispatch(logout());
        }
      })
    );

  }

}
