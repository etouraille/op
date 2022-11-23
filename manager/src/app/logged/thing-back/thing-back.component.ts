import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {switchMap} from "rxjs";

@Component({
  selector: 'app-thing-back',
  templateUrl: './thing-back.component.html',
  styleUrls: ['./thing-back.component.scss']
})
export class ThingBackComponent extends SubscribeComponent implements OnInit {

  constructor(
    private http: HttpClient,
  ) {
    super();
  }
  user: any = null;
  thingsOut: any[] = [];
  ngOnInit(): void {
  }

  selectUserId(id: number) {
    this.add(this.http.get('api/users/' + id).pipe(switchMap((user: any) => {
      this.user = user;
      return this.http.get('api/things-out?userId=' + id)
    })).subscribe((things: any) => {
      this.thingsOut = things['hydra:member'];
    }))
  }

  back(thing: any, i: number) {
    let reservation = thing.reservations.find((elem: any) => elem.state === 1);
    let index = thing.reservations.findIndex((elem: any) => elem.state === 1)
    this.add(this.http.patch('api/reservations/' + reservation.id ,
      {state: 2, backDate: new Date()}).pipe(switchMap((reservation: any) => {
      this.thingsOut[i].reservations[index] = reservation;
      this.selectUserId(this.user.id);
      return this.http.put('api/thing-back', thing);
    })).subscribe((data: any) => {
      console.log(data);
    }));
  }
}
