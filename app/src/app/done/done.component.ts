import { Component, OnInit } from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {ToastrService} from "ngx-toastr";
import {SubscribeComponent} from "../../lib/component/subscribe/subscribe.component";
import {Store} from "@ngrx/store";

@Component({
  selector: 'app-done',
  templateUrl: './done.component.html',
  styleUrls: ['./done.component.scss']
})
export class DoneComponent extends SubscribeComponent implements OnInit {

  things: any[] = [];
  isMember: boolean = false;
  total: number = 0;

  constructor(
    private http: HttpClient,
    private store: Store<{login: any}>
  ) {
    super();
  }

  ngOnInit(): void {
    this.add(
      this.http.get('api/done').subscribe((data: any) => {
        this.things = data['hydra:member'];
        this.things = this.things.map((thing: any) => ({ ...thing, reservations: thing.reservations.filter((reservation:any) => reservation.state === 2)}));
        this.total = this.things.reduce((a: number, thing: any) => (isNaN(thing.dailyPrice) ? a: a + thing.dailyPrice * thing.reservations[0].delta), 0);
      })
    )
    this.add(
      this.store.select((data: any) => data.login.user?.roles?.includes('ROLE_MEMBER').subscribe((isMember: boolean) => {
        this.isMember = isMember;
      }))
    );

  }
}
