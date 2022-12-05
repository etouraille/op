import { Component, OnInit } from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {ToastrService} from "ngx-toastr";
import {SubscribeComponent} from "../../lib/component/subscribe/subscribe.component";
import * as moment from 'moment';

@Component({
  selector: 'app-current',
  templateUrl: './current.component.html',
  styleUrls: ['./current.component.scss']
})
export class CurrentComponent extends SubscribeComponent implements OnInit {

  things: any[] = [];

  constructor(
    private http: HttpClient,
  ) {
    super();
  }

  ngOnInit(): void {
    this.add(
      this.http.get('api/current').subscribe((data: any) => {
        this.things = data['hydra:member'];
        this.things = this.things.map((thing: any) => ({ ...thing, reservations: thing.reservations.filter((reservation:any) => reservation.state === 1)}));
      })
    )
  }

  pastDate(endDate: Date) {
    return moment().isSameOrAfter(moment(endDate),'day');
  }
}
