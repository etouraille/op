import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {NgbModal, NgbModalRef} from "@ng-bootstrap/ng-bootstrap";
import {CalendarComponent} from "../../../lib/component/calendar/calendar.component";
import {Router} from "@angular/router";
import {switchMap} from "rxjs";
import {Store} from "@ngrx/store";

@Component({
  selector: 'app-thing-out',
  templateUrl: './thing-out.component.html',
  styleUrls: ['./thing-out.component.scss']
})
export class ThingOutComponent extends SubscribeComponent implements OnInit {

  user: any = null;
  things: any[] = [];
  waiting: any[] = [];
  ref: NgbModalRef|null = null;
  skipThings: any[] = [];
  payment: boolean = false;
  constructor(
    private http: HttpClient,
    private modalService: NgbModal,
    private router: Router,
    private store: Store<{login: any}>
  ) {
    super();
  }



  ngOnInit(): void {
    this.add(this.store.select((data: any) => data.login.payment).subscribe(payment => {
      this.payment = payment
    }));
  }

  changeUserId($event: number) {
    this.add(this.http.get('api/users/' + $event).pipe(switchMap((user: any) => {
      this.user = user;
      this.things = [];
      return this.http.get('api/waiting?userId=' + this.user.id);
    })).subscribe((data: any)=> {
      this.waiting = data['hydra:member'].map((thing: any) => {
        let reservation = thing.reservations.find((reservation:any) => !reservation.state || reservation.state == -1);
        return {
          ...thing,
          startDate: reservation.startDate,
          endDate: reservation.endDate,
          owner: reservation.owner['@id'],
          reservationId: reservation.id
        }
      });
      this.skipThings = this.waiting.slice();
    }))
  }

  changeThingId($event: number) {
    this.add(this.http.get('api/things/' + $event).subscribe((data: any) => {
      this.things.push(data);
      this.skipThings.push(data);
    }))
  }

  removeThing(i: number, id: number) {
    this.things.splice(i, 1);
    let index = this.skipThings.findIndex(elem => elem.id = id);
    this.skipThings.splice(index, 1);
  }

  removeWaiting(i: number, id: number, reservationId: number) {
    this.add(this.http.delete('api/reservations/' + reservationId).subscribe((reservation: any) => {
      this.waiting.splice(i,1);
      this.skipThings.splice(this.skipThings.findIndex(elem => elem.id = id), 1);
    }))
  }

  book(thing: any, index: number) {
    this.ref = this.modalService.open(CalendarComponent);
    this.ref.componentInstance.reservations = thing.reservations;
    this.ref.componentInstance.userId = this.user.id;
    this.ref.result.then((dates: any) => {
      this.things[index].owner = dates.owner;
      this.things[index].startDate = dates.startDate;
      this.things[index].endDate = dates.endDate;
    })
  }

  finishDisabled() {
    let disabled = false;
    this.things.forEach((thing: any) => {
      if(!thing.startDate) {
        disabled = true;
      }
    })
    return disabled;
  }

  finish() {
    Promise.all(this.things.concat(this.waiting).map((thing: any) => {
        let reservation : any  = {
          owner: thing.owner,
          thing: 'api/things/' + thing.id ,
          startDate: thing.startDate,
          endDate: thing.endDate,
          state: 1,
        }
        if(thing.reservationId) {
          reservation['id'] = thing.reservationId;
          return this.http.patch('api/reservations/' + thing.reservationId, reservation).toPromise()
        } else {
          return this.http.post('api/reservations', reservation).toPromise();
        }
      })
    ).then(() => this.router.navigate(['logged/thing-list']));
  }
}
