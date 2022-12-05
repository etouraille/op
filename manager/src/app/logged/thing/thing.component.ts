import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../../lib/component/subscribe/subscribe.component";
import {ActivatedRoute} from "@angular/router";
import {of, switchMap} from "rxjs";
import {HttpClient} from "@angular/common/http";
import {NgbModal, NgbModalRef} from "@ng-bootstrap/ng-bootstrap";
import {CalendarComponent} from "../../../lib/component/calendar/calendar.component";
import {WhoComponent} from "../../../lib/component/who/who.component";
import {WhoModalComponent} from "../../../lib/component/who-modal/who-modal.component";
import {FormControl} from "@angular/forms";
import {ToastrService} from "ngx-toastr";

@Component({
  selector: 'app-thing',
  templateUrl: './thing.component.html',
  styleUrls: ['./thing.component.scss']
})
export class ThingComponent extends SubscribeComponent implements OnInit {

  thing: any = {};
  ref: NgbModalRef|null = null;
  refWho: NgbModalRef|null = null;
  state: FormControl = new FormControl<any>(null);
  constructor(
    private route: ActivatedRoute,
    private http: HttpClient,
    private modalService: NgbModal,
    private toastR: ToastrService,
    ){
    super();
  }

  ngOnInit(): void {
    this.add(this.route.paramMap.pipe(switchMap((param: any) => {
      let id = param.get('id')
      return this.http.get<{state: string}>('api/things/' +id )
    })).subscribe((data: {state: string}) => {
      this.thing = data;
      this.state.patchValue(this.thing.status==='active')
    }));
    this.add(this.state.valueChanges.pipe(
      switchMap((state: boolean) => {
        if(typeof state === 'boolean') {
          let obj: any = {status: 'inactive'}
          if (state) {
            obj.status = 'active';
          }
          return this.http.patch('api/things/' + this.thing.id, obj);
        } else {
          return of(false);
        }
      }
      )).subscribe());
  }

  openModal() {
    this.refWho = this.modalService.open(WhoModalComponent);
    this.refWho.result.then((dataId: any) => {
      this.ref = this.modalService.open(CalendarComponent);
      this.ref.componentInstance.reservations = this.thing.reservations;
      this.ref.componentInstance.userId = dataId.id;
      this.ref.componentInstance._borrow = true;
      this.ref.result.then((subs: any) => {
        let obj = {...subs, owner: 'api/users/' + dataId.id, state: 1 , thing: 'api/things/' + this.thing.id };
        this.add(this.http.post('api/reservations', obj).subscribe(reservation => {
          this.thing.reservations.push(reservation);
          this.toastR.success('Vous venez d\'effectuer une reservation');
        }))
      }, (reason: any) => {
        console.log(reason);
      })
    },(reason: any) => console.log(reason))
  }

  isBackable(): boolean {
    return !!(this.thing.reservations ? this.thing.reservations.find((elem: any) => elem.state === 1): false);
  }

  back() {
    let backReservation = this.thing.reservations.find((elem: any) => elem.state === 1);
    backReservation.state = 2;
    this.add(this.http.put('api/things/' + this.thing.id +'/reservations/' + backReservation.id, this.thing).subscribe((data: any) => {
      this.toastR.success('Vous venez de retourner un objet');
    }))

  }
}
