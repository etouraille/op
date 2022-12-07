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
import {ActivatedRoute, Router} from "@angular/router";
import {ToastrService} from "ngx-toastr";
import {ReservationService} from "../../lib/service/reservation.service";
import {PingService} from "../../lib/service/ping.service";
import {environment} from "../../environments/environment";
import {Location} from "@angular/common";
import * as _ from 'lodash';

@Component({
  selector: 'app-things',
  templateUrl: './things.component.html',
  styleUrls: ['./things.component.scss']
})
export class ThingsComponent extends SubscribeComponent implements OnInit {

  things: any[] = [];
  stars: any[] = [];
  lasts: any[] = [];
  types: any[] = [];
  rands: any[] = [];
  proposed: any[] = [];
  modalRef: any = null;
  logged: boolean = false;
  isMember: boolean = false;
  cdn: string = environment.cdn;
  user: any = null;
  payment: boolean = false;
  checked: any[] = [];

  constructor(
    private http: HttpClient,
    private service: NgbModal,
    private store: Store<{logged: boolean}>,
    private router: Router,
    private toastR: ToastrService,
    private reservationService: ReservationService,
    private pingService: PingService,
    private location: Location,
    private route: ActivatedRoute,
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
      this.payment = data.payment;
    }));
    this.add(this.pingService.ping());
    this.redirectOnCardIfLoggedAndNoCard();
    this.add(this.http.get('api/proposed').subscribe((data: any) => {
      this.proposed = data['hydra:member'];
    }))
    this.add(this.http.get('api/thing_types').pipe(switchMap((data: any) => {
      this.types = data['hydra:member'];
      return this.route.queryParams;
    })).subscribe((param: any) => {
      console.log(' subscribe to query params');
      this.getCategories(param.filter);
      this.types = this.types.map((type: any) => ({ ...type, selected: param?.filter?.split(',').map((id: string) => parseInt(id))?.includes( type.id)}));
      this.checked = param.filter ? _.uniq(param?.filter?.split(',').map((id: string) => parseInt(id))): [];
    }))


  }

  getCategories(filter: any) {
    this.add(this.http.get(filter ? 'api/stars?filter=' + filter : 'api/stars').pipe(
      switchMap((data:any) => {
      this.stars = data['hydra:member'].map((elem: any) =>  elem[0]);
      return this.http.get(filter ? 'api/lasts?filter=' + filter : 'api/lasts')
    }), switchMap((data:any) => {
        this.lasts = data['hydra:member'];
        // on exclue les entité deja présentés dans stars. pour ca qu'on en a pris le double.
        this.lasts = this.lasts.filter((last:any)=> !this.stars.map(elem => elem.id).includes(last.id)).slice(0,4);
      return this.http.get(filter ? 'api/thing/all?filter=' + filter : 'api/thing/all')
      })).subscribe((data: any) => {
        this.rands = data['hydra:member'].filter((data: any) => !this.stars.map(elem =>elem.id).includes(data.id) && !this.lasts.map(elem =>elem.id).includes(data.id)).splice(0,4)
    }));

  }

  openModal(thing: any, event: Event) {
    event.stopPropagation();
    this.modalRef = this.service.open(CalendarComponent);
    this.modalRef.componentInstance.reservations = thing.reservations;
    this.modalRef.componentInstance.readOnly = !(this.logged && !this.user?.roles?.includes('ROLE_MEMBER') || this.user?.roles?.includes('ROLE_MEMBER') && this.user.isMemberValidated)
    this.modalRef.componentInstance.payment = this.payment;
    this.modalRef.result.then((dates: any) => {
      this.add(this.reservationService.book(dates, thing, this.payment))
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

  changeRadio($event: any, id: any) {
    let filter = $event?.target?.checked;
    if(typeof $event === 'boolean') {
      filter = $event;
    }
    this.checked = _.uniq(this.checked);
    if(filter) {
      this.checked.push(parseInt(id));
    } else {
      this.checked.splice(this.checked.indexOf(parseInt(id)), 1);
    }
    this.location.go('/?filter=' + this.checked.join(','));
    this.getCategories(this.checked.join(','));
  }
}
