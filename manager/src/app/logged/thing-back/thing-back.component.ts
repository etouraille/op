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

    //TODO a revoir, mettre tout dans une même transaction.
    let reservation = thing.reservations.find((elem: any) => elem.state === 1);
    this.add(this.http.put('api/things/' + thing.id +'/reservations/' + reservation.id, thing).subscribe((data: any) => {
      this.thingsOut.splice(this.thingsOut.findIndex((thing: any) => thing.id = data.id),1);
    }))
  }
}
