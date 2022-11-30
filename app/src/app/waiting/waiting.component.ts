import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {ToastrService} from "ngx-toastr";

@Component({
  selector: 'app-waiting',
  templateUrl: './waiting.component.html',
  styleUrls: ['./waiting.component.scss']
})
export class WaitingComponent extends SubscribeComponent implements OnInit {
  things: any[] = [];

  constructor(
    private http: HttpClient,
    private toastR: ToastrService,
  ) {
    super();
  }

  ngOnInit(): void {
    this.add(
      this.http.get('api/waiting').subscribe((data: any) => {
        this.things = data['hydra:member'];
        this.things = this.things.map((thing: any) => ({ ...thing, reservations: thing.reservations.filter((reservation:any) => !!!reservation.state )}));
      })
    )
  }

  cancel(id:number, i: number) {
    this.add(this.http.delete('api/reservations/' + id).subscribe((data: any) => {
      this.toastR.success('Reservation annulée');
      this.things.splice(i,1);
    }, (error: any) => {
      this.toastR.error('Annulation impossible');
    }))

  }
}
