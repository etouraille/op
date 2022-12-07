import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {ToastrService} from "ngx-toastr";

@Component({
  selector: 'app-compensation',
  templateUrl: './compensation.component.html',
  styleUrls: ['./compensation.component.scss']
})
export class CompensationComponent extends SubscribeComponent implements OnInit {

  constructor(
    private http: HttpClient,
    private toastR: ToastrService,
  ) {
    super();
  }

  private thingId: number|undefined;
  private userId: number|undefined
  user: any = null;
  thing: any = null;
  rate: number|undefined;
  compensations: any[] = [];

  ngOnInit(): void {
    this.add(this.http.get('api/compensations').subscribe((compensations: any) => {
      this.compensations = compensations['hydra:member'];
    }))
  }

  canAdd(): boolean {
    return !!this.userId && !!this.thingId && !!this.rate;
  }

  selectThing($event: number) {
    this.thingId = $event;
    this.add(this.http.get('api/things/' + $event).subscribe((thing: any) => {
      this.thing = thing;
    }))
  }

  selectUser($event: number) {
    this.userId = $event;
    this.add(this.http.get('api/users/' + $event).subscribe((user: any) => {
      this.user = user;
    }))
  }

  submit() {
    this.add(
      this.http.post(
        'api/compensations',
        {
          user: 'api/users/' + this.userId,
          thing: 'api/things/' + this.thingId,
          rate: parseFloat('' + this.rate),
        }).subscribe((data: any) => {
          if(data.error) {
            this.toastR.error(data.error);
          } else {
            this.toastR.success('Création d\'une nouvelle compensation');
            this.ngOnInit();
          }
      }))
  }
}
