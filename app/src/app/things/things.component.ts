import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {BsModalRef, BsModalService} from "ngx-bootstrap/modal";
import {CalendarComponent} from "../../lib/component/calendar/calendar.component";
import {NgbModal} from "@ng-bootstrap/ng-bootstrap";
import {Store} from "@ngrx/store";
import { user as setUser } from '../../lib/actions/user-action'
import {logout} from "../../lib/actions/login-action";
import {of, switchMap, tap} from "rxjs";
import {Router} from "@angular/router";
import {ToastrService} from "ngx-toastr";
import {ReservationService} from "../../lib/service/reservation.service";
import {PingService} from "../../lib/service/ping.service";

@Component({
  selector: 'app-things',
  templateUrl: './things.component.html',
  styleUrls: ['./things.component.scss']
})
export class ThingsComponent extends SubscribeComponent implements OnInit {

  things: any[] = [];
  modalRef: any = null;
  logged: boolean = false;
  isMember: boolean = false;
  constructor(
    private http: HttpClient,
    private service: NgbModal,
    private store: Store<{logged: boolean}>,
    private router: Router,
    private toastR: ToastrService,
    private reservationService: ReservationService,
    private pingService: PingService,
  ) {
    super();
  }

  ngOnInit(): void {
    this.add(this.http.get('api/things?name=&description=').subscribe((data:any) => {
      this.things = data['hydra:member'];
    }))
    this.add(this.store.select((state:any) => state.login.logged).subscribe(data => {
      this.logged = data;

    }));
    this.add(this.pingService.ping());
    this.redirectOnCardIfLoggedAndNoCard();
  }

  openModal(index: number) {
    this.modalRef = this.service.open(CalendarComponent);
    this.modalRef.componentInstance.reservations = this.things[index].reservations;
    this.modalRef.result.then((dates: any) => {
      this.add(this.reservationService.book(dates, this.things[index]))
    }, (reason: any) => {

    });
  }

  redirectOnCardIfLoggedAndNoCard() {
    this.add(this.store.select((data:any) => data.login)
      .pipe(
        switchMap((login:any) => {
          if(!login.logged) {
            return of([0,1,2]);
          }
          else {
            this.isMember = login?.user?.roles.includes('ROLE_MEMBER');
            return this.http.get<any[]>('api/available/card')
          }
        }),
        tap((array: any[]) => {
          if(array.length === 0) {
            this.add(this
              .toastR
              .error(this.isMember ? 'Rajouter une carte pour pouvoir payer la dégradation des objets': 'Rajouter une carte pour pouvoir payer en boutique')
              .onTap
              .subscribe(() => this.router.navigate(['card']))
            );
          }
        })
      ).subscribe()
    );
  }
}
