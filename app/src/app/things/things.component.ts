import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {BsModalRef, BsModalService} from "ngx-bootstrap/modal";
import {CalendarComponent} from "../../lib/component/calendar/calendar.component";
import {NgbModal} from "@ng-bootstrap/ng-bootstrap";
import {Store} from "@ngrx/store";
import { user as setUser } from '../../lib/actions/user-action'
import {logout} from "../../lib/actions/login-action";

@Component({
  selector: 'app-things',
  templateUrl: './things.component.html',
  styleUrls: ['./things.component.scss']
})
export class ThingsComponent extends SubscribeComponent implements OnInit {

  things: any[] = [];
  modalRef: any = null;
  logged: boolean = false;
  constructor(
    private http: HttpClient,
    private service: NgbModal,
    private store: Store<{logged: boolean}>
  ) {
    super();
  }

  ngOnInit(): void {
    this.add(this.http.get('api/things?name=&description=').subscribe((data:any) => {
      this.things = data['hydra:member'];
    }))
    this.add(this.store.select((state:any) => state.login.logged).subscribe(data => {
      this.logged = data;
      console.log( data);
    }));
    this.add(
      this.http.get('api/ping').subscribe((user: any) => {
        if(user.roles) {
          this.store.dispatch(setUser({user}))
        } else {
          this.store.dispatch(logout());
        }
      })
    )

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
