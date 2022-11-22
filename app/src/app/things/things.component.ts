import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {BsModalRef, BsModalService} from "ngx-bootstrap/modal";
import {CalendarComponent} from "../../lib/component/calendar/calendar.component";
import {NgbModal} from "@ng-bootstrap/ng-bootstrap";

@Component({
  selector: 'app-things',
  templateUrl: './things.component.html',
  styleUrls: ['./things.component.scss']
})
export class ThingsComponent extends SubscribeComponent implements OnInit {

  things: any[] = [];
  modalRef: any = null;
  constructor(
    private http: HttpClient,
    private service: NgbModal,
  ) {
    super();
  }

  ngOnInit(): void {
    this.add(this.http.get('api/things?name=&description=').subscribe((data:any) => {
      this.things = data['hydra:member'];
    }))
  }

  openModal(index: number) {
    this.modalRef = this.service.open(CalendarComponent);
    this.modalRef.componentInstance.reservations = this.things[index].reservations;
    this.modalRef.result.then((dates: any) => {
      if(!dates.endDate) {
        dates.endDate = dates.startDate;
      }
      dates = Object.assign(dates, { thing:  'api/things/' + this.things[index].id });
      this.add(this.http.post('api/reservations', dates).subscribe((reservation) => {
        this.things[index].reservations.push(reservation);
      }));
    });

  }
}
