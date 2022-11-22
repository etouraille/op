import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {Router} from "@angular/router";

@Component({
  selector: 'app-thing-list',
  templateUrl: './thing-list.component.html',
  styleUrls: ['./thing-list.component.scss']
})
export class ThingListComponent extends SubscribeComponent implements OnInit {

  things: any[] = [];
  constructor(
    private http: HttpClient,
    private router: Router
  ) {
    super()
  }

  ngOnInit(): void {
    this.add(this
      .http
      .get("api/things?name=&description=")
      .subscribe( (data: any) => {
        this.things = data['hydra:member'];
      }
    ));
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
}
