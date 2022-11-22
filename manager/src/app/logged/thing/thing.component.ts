import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../../lib/component/subscribe/subscribe.component";
import {ActivatedRoute} from "@angular/router";
import {switchMap} from "rxjs";
import {HttpClient} from "@angular/common/http";
import {NgbModal, NgbModalRef} from "@ng-bootstrap/ng-bootstrap";
import {CalendarComponent} from "../../../lib/component/calendar/calendar.component";
import {WhoComponent} from "../../../lib/component/who/who.component";
import {WhoModalComponent} from "../../../lib/component/who-modal/who-modal.component";

@Component({
  selector: 'app-thing',
  templateUrl: './thing.component.html',
  styleUrls: ['./thing.component.scss']
})
export class ThingComponent extends SubscribeComponent implements OnInit {

  thing: any = {};
  ref: NgbModalRef|null = null;
  refWho: NgbModalRef|null = null;
  constructor(
    private route: ActivatedRoute,
    private http: HttpClient,
    private modalService: NgbModal,
    ){
    super();
  }

  ngOnInit(): void {
    this.add(this.route.paramMap.pipe(switchMap((param: any) => {
      let id = param.get('id')
      return this.http.get('api/things/' +id )
    })).subscribe((data: any) => {
      this.thing = data;
    }))
  }

  openModal() {
    this.refWho = this.modalService.open(WhoModalComponent);
    this.refWho.result.then((dataId: any) => {
      this.ref = this.modalService.open(CalendarComponent);
      this.ref.componentInstance.reservations = this.thing.reservations;
      this.ref.componentInstance.userId = dataId.id;
      this.ref.result.then((subs: any) => {
        let obj = {...subs, owner: 'api/users/' + dataId.id, state: 1 , thing: 'api/things/' + this.thing.id };
        this.add(this.http.post('api/reservations', obj).subscribe(reservation => {
          this.thing.reservations.push(reservation);
        }))
      }, (reason: any) => {
        console.log(reason);
      })
    })
  }

  isBackable(): boolean {
    return !!(this.thing.reservations ? this.thing.reservations.find((elem: any) => elem.state === 1): false);
  }

  back() {
    let backReservation = this.thing.reservations.find((elem: any) => elem.state === 1);
    backReservation.state = 2;
    this.add(
      this.http.patch('api/reservations/' + backReservation.id , {state: 2, backDate: new Date()}).subscribe(() => {
        let index = this.thing.reservations.findIndex((elem: any) => elem.state == 1);
        this.thing.reservations[index].state = 2;
      })
    );
  }
}
