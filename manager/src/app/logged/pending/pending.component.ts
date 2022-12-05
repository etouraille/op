import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";

@Component({
  selector: 'app-pending',
  templateUrl: './pending.component.html',
  styleUrls: ['./pending.component.scss']
})
export class PendingComponent extends SubscribeComponent implements OnInit {

  things: any[] = [];
  since: number = 15;

  constructor(
    private http: HttpClient,
  ) {
    super();
  }

  ngOnInit(): void {
    this.changeValue(this.since);
  }

  changeValue($event: any) {
    this.add(
      this.http.get('api/pending?delta=' + $event).subscribe((things: any) => {
        this.things = things['hydra:member'];
        this.things.map(thing => ({ ...thing, reservations: thing.reservations.filter( (reservation: any) => reservation.state === 1 )}))
      })
    )
  }
}
