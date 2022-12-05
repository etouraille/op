import { Component, OnInit } from '@angular/core';
import {Router} from "@angular/router";
import {SubscribeComponent} from "../../lib/component/subscribe/subscribe.component";
import {Store} from "@ngrx/store";
import {tap} from "rxjs";
import {HttpClient} from "@angular/common/http";
import { available, unavailable } from "../../lib/actions/payment-action";

@Component({
  selector: 'app-logged',
  templateUrl: './logged.component.html',
  styleUrls: ['./logged.component.scss']
})
export class LoggedComponent extends SubscribeComponent implements OnInit {

  constructor(
    private router: Router,
    private store: Store<{logged: boolean}>,
    private http: HttpClient,
  ) {
    super();
  }

  ngOnInit(): void {
    this.add(
      this.store.select((data: any) => data.login).pipe(tap((data: any) => {
        if(!data.logged) {
          this.router.navigate(['login']);
        }
      })).subscribe()
    );
    this.add(this.http.get('payment/front').subscribe((data: any) => {
      if(data.available) {
        this.store.dispatch(available());
      } else {
        this.store.dispatch(unavailable());
      }
    }))

  }

  onSelected($event: number) {
    this.router.navigate(['logged/thing/' + $event] );
  }
}
