import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {NgbModal, NgbModalRef} from "@ng-bootstrap/ng-bootstrap";
import {CalendarComponent} from "../../../lib/component/calendar/calendar.component";
import {Router} from "@angular/router";

@Component({
  selector: 'app-thing-out',
  templateUrl: './thing-out.component.html',
  styleUrls: ['./thing-out.component.scss']
})
export class ThingOutComponent extends SubscribeComponent implements OnInit {

  user: any = null;
  things: any[] = [];
  ref: NgbModalRef|null = null;
  constructor(
    private http: HttpClient,
    private modalService: NgbModal,
    private router: Router,
  ) {
    super();
  }



  ngOnInit(): void {
  }

  changeUserId($event: number) {
    this.add(this.http.get('api/users/' + $event).subscribe((data: any) => {
      this.user = data;
      this.things = [];
    }))
  }

  changeThingId($event: number) {
    this.add(this.http.get('api/things/' + $event).subscribe((data: any) => {
      this.things.push(data);
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
    Promise.all(this.things.map((thing: any) => {
        let reservation = {
          owner: thing.owner,
          thing: 'api/things/' + thing.id ,
          startDate: thing.startDate,
          endDate: thing.endDate,
          state: 1,
        }
        return this.http.post('api/reservations', reservation).toPromise();
      })
    ).then(() => this.router.navigate(['logged/thing-list']));
  }
}
