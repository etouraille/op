import { Component, OnInit } from '@angular/core';
import {Router} from "@angular/router";
import {SubscribeComponent} from "../../lib/component/subscribe/subscribe.component";
import {Store} from "@ngrx/store";
import {tap} from "rxjs";

@Component({
  selector: 'app-logged',
  templateUrl: './logged.component.html',
  styleUrls: ['./logged.component.scss']
})
export class LoggedComponent extends SubscribeComponent implements OnInit {

  constructor(
    private router: Router,
    private store: Store<{logged: boolean}>
  ) {
    super();
  }

  ngOnInit(): void {
    this.add(
      this.store.select((data: any) => { console.log( data); return data.login.logged}).pipe(tap((logged: boolean) => {
        console.log( logged);
        if(!logged) {
          this.router.navigate(['login']);
        }
      })).subscribe()
    );
  }

  onSelected($event: number) {
    this.router.navigate(['logged/thing/' + $event] );
  }
}
