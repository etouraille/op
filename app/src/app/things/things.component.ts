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
import {environment} from "../../environments/environment";

@Component({
  selector: 'app-things',
  templateUrl: './things.component.html',
  styleUrls: ['./things.component.scss']
})
export class ThingsComponent extends SubscribeComponent implements OnInit {

  things: any[] = [];
  stars: any[] = [];
  lasts: any[] = [];
  proposed: any[] = [];
  modalRef: any = null;
  logged: boolean = false;
  isMember: boolean = false;
  cdn: string = environment.cdn;
  private user: any = null;

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
    this.add(this.store.select((state:any) => state.login).subscribe(data => {
      this.logged = data.logged;
      this.user = data.user;

    }));
    this.add(this.pingService.ping());
    this.redirectOnCardIfLoggedAndNoCard();
    this.add(this.http.get('api/stars').pipe(switchMap((data:any) => {
      this.stars = data['hydra:member'].map((elem: any) =>  elem[0]);
      return this.http.get('api/lasts')
    })).subscribe((data: any) => {
      this.lasts = data['hydra:member'];
      // on exclue les entité deja présentés dans stars. pour ca qu'on en a pris le double.
      this.lasts = this.lasts.filter((last:any)=> !this.stars.map(elem => elem.id).includes(last.id)).slice(0,4);
    }));
    this.add(this.http.get('api/proposed').subscribe((data: any) => {
      this.proposed = data['hydra:member'];
    }))
  }

  openModal(thing: any) {
    this.modalRef = this.service.open(CalendarComponent);
    this.modalRef.componentInstance.reservations = thing.reservations;
    this.modalRef.componentInstance.readOnly = !(this.logged && !this.user?.roles?.includes('ROLE_MEMBER') || this.user?.roles?.includes('ROLE_MEMBER') && this.user.isMemberValidated)
    this.modalRef.result.then((dates: any) => {
      this.add(this.reservationService.book(dates, thing))
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

  navigate(id: number) {
    this.router.navigate(['thing/' + id]);

  }
}
