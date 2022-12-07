import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {ToastrService} from "ngx-toastr";
import {Store} from "@ngrx/store";
import {decrease, increase, set} from "../../lib/actions/book-action";
import {Router} from "@angular/router";
import * as moment from 'moment';

@Component({
  selector: 'app-waiting',
  templateUrl: './waiting.component.html',
  styleUrls: ['./waiting.component.scss']
})
export class WaitingComponent extends SubscribeComponent implements OnInit {
  things: any[] = [];
  payment: boolean = false;
  isMember: boolean = false;

  constructor(
    private http: HttpClient,
    private toastR: ToastrService,
    private store: Store<{login: any}>,
    private router: Router,
  ) {
    super();
  }

  ngOnInit(): void {
    this.getWaiting();
    this.add(this.store.select((data: any) => data.login).subscribe((data: any) => {
      this.payment = data.payment;
      this.isMember = data.user?.roles.includes('ROLE_MEMBER');
    }))
  }

  getWaiting() {
    this.add(
      this.http.get('api/waiting').subscribe((data: any) => {
        this.things = data['hydra:member'];
        this.things = this.things.map((thing: any) => ({ ...thing, reservations: thing.reservations.filter((reservation:any) => !reservation.state || reservation.state === -2 )}));
      })
    )
  }

  cancel(id:number, i: number) {
    this.add(this.http.delete('api/reservations/' + id).subscribe((data: any) => {
      this.toastR.success('Reservation annulée');
      this.things.splice(i,1);
      this.store.dispatch(decrease());
    }, (error: any) => {
      this.toastR.error('Annulation impossible');
    }))

  }

  pay() {
    let datesInFuture = this.things.reduce((a: boolean, thing: any) => a && moment(thing.reservations[0].startDate).isSameOrAfter(moment(), 'day'), true);
    if( ! datesInFuture ) {
      this.toastR.error('Il y a une date de reservation qui est dépassée');
    } else {
      this.add(this.http.put('api/pay', {data: 1}).subscribe((data: any) => {
        this.getWaiting();
        if(data.success) {
          this.store.dispatch(set({quantity: 0}));
        }
        if (!data.success) {
          this.toastR.error(data.error);
          if(data.id) {
            this.router.navigate(['card-confirm/' + data.id]);
          }
        }

      }))
    }
  }
}
