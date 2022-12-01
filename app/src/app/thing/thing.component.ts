import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {ActivatedRoute} from "@angular/router";
import {switchMap} from "rxjs";
import {environment} from "../../environments/environment";
import {Store} from "@ngrx/store";
import {BsModalRef, BsModalService} from "ngx-bootstrap/modal";
import {CalendarComponent} from "../../lib/component/calendar/calendar.component";
import {NgbModal, NgbModalRef} from "@ng-bootstrap/ng-bootstrap";
import {ReservationService} from "../../lib/service/reservation.service";

@Component({
  selector: 'app-thing',
  templateUrl: './thing.component.html',
  styleUrls: ['./thing.component.scss']
})
export class ThingComponent extends SubscribeComponent implements OnInit {

  thing: any = null;
  cdn: any = null;
  isLogged: boolean = false;
  ref: NgbModalRef | undefined;
  constructor(
    private http: HttpClient,
    private route: ActivatedRoute,
    private store: Store<{ login: any}>,
    private modal: NgbModal,
    private reservationService: ReservationService,
  ) {
    super();
    this.cdn = environment.cdn;
  }

  ngOnInit(): void {
    this.add(
      this.route.paramMap.pipe(
        switchMap((param: any) => {
          let id = param.get('id');
          return this.http.get('api/things/' + id)
        })
      ).subscribe((thing: any) => {
        console.log(thing);
        this.thing = thing;
      })
    )
    this.add(this.store.select((data: any) => data.login.logged).subscribe((logged: boolean) => {
      this.isLogged = logged;
    }))
  }

  book() {
    this.ref = this.modal.open(CalendarComponent);
    this.ref.componentInstance.reservations = this.thing.reservations;
    this.ref.componentInstance.readOnly = !this.isLogged;
    this.ref.result.then((dates: any) => {
      this.add(this.reservationService.book(dates, this.thing))
    },reason => console.log(reason));
  }
}
