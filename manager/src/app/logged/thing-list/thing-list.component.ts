import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {Router} from "@angular/router";
import {ToastrService} from "ngx-toastr";

@Component({
  selector: 'app-thing-list',
  templateUrl: './thing-list.component.html',
  styleUrls: ['./thing-list.component.scss']
})
export class ThingListComponent extends SubscribeComponent implements OnInit {

  things: any[] = [];
  types: any[] = [];
  constructor(
    private http: HttpClient,
    private router: Router,
    private toastR: ToastrService,
  ) {
    super()
  }

  ngOnInit(): void {
    this.add(this
      .http
      .get("api/thing/all")
      .subscribe( (data: any) => {
        this.things = data['hydra:member'];
      }
    ));
    this.add(this.http.get('api/thing_types').subscribe((data: any) => this.types = data['hydra:member']))
  }

  delete(id: any) {
    this.add(
      this
        .http
        .delete('api/things/' + id )
        .subscribe(data => {
          this.ngOnInit();
        })
    )
  }

  edit(id: string) {
    this.router.navigate(['logged/thing-edit/' + id]);
  }

  changeStatus(id: number, $event: any) {
    let obj : any = { status: 'inactive'};
    if($event) {
      obj.status = 'active';
    }
    this.add(
      this.http.patch('api/things/' + id, obj).subscribe(() => {
        this.toastR.success($event? 'L objet est maintenant disponible sur le front': 'L\'objet n\'est plus disponible sur le front')
      }, (error: any) => {
        this.toastR.error('Erreur lors de la modification de l\'état' + error.error['hydra:description']);
      })
    )
  }

  changeType($event: any, id: number) {
    this.add(this.http.patch('api/things/' + id , { type: '/api/thing_types/' + $event}).subscribe(() => {
      this.toastR.success('Type définié');
    }, (error) => {
      this.toastR.error('Erreur dans la modification du type');
    }));
  }
}
