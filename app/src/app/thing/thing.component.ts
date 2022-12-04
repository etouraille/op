import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {ActivatedRoute, Router} from "@angular/router";
import {of, switchMap} from "rxjs";
import {environment} from "../../environments/environment";
import {Store} from "@ngrx/store";
import {BsModalRef, BsModalService} from "ngx-bootstrap/modal";
import {CalendarComponent} from "../../lib/component/calendar/calendar.component";
import {NgbModal, NgbModalRef} from "@ng-bootstrap/ng-bootstrap";
import {ReservationService} from "../../lib/service/reservation.service";
import {PingService} from "../../lib/service/ping.service";

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
  user: any = null;
  payment: boolean = false;
  constructor(
    private http: HttpClient,
    private route: ActivatedRoute,
    private router: Router,
    private store: Store<{ login: any}>,
    private modal: NgbModal,
    private reservationService: ReservationService,
    private pingService: PingService,

  ) {
    super();
    this.cdn = environment.cdn;
  }

  ngOnInit(): void {

    let url :string ;

    this.add(
      this.route.paramMap.pipe(
        switchMap((param: any) => {
          let id = param.get('id');
          url = param.get('url');
          if(this.router.url.match(/\/thing/)) {
            return of(id);
          } else {
            return this.http.get('api/url');
          }
        }),
        switchMap((idOrObject: any) => {
          if(typeof(idOrObject) === 'string') {
            return this.http.get('api/things/' + idOrObject);
          } else {
            let data = idOrObject['hydra:member'];
            let index = data.findIndex((elem :any) => elem.url === url);
            if(index< 0) {
              this.router.navigate(['/']);
              return of(null);
            } else {
              return this.http.get('api/things/' + data[index].id);
            }
          }
        })
      ).subscribe((thing: any) => {
        this.thing = thing;
      })
    )
    this.add(this.store.select((data: any) => data.login).subscribe((data: any) => {
      this.isLogged = data.logged;
      this.user = data.user;
      this.payment = data.payment;
    }))
    this.add(this.pingService.ping());
  }

  book()  {
    this.ref = this.modal.open(CalendarComponent);
    this.ref.componentInstance.reservations = this.thing.reservations;
    this.ref.componentInstance.readOnly = !( this.isLogged && !this.user?.roles?.includes('ROLE_MEMBER') || (this.user?.roles?.includes('ROLE_MEMBER') && this.user.isMemberValidated ));
    this.ref.componentInstance.payment = this.payment;
    this.ref.result.then((dates: any) => {
      this.add(this.reservationService.book(dates, this.thing, this.payment))
    },reason => console.log(reason));
  }
}
