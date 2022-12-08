import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {environment} from "../../environments/environment";
import {DomSanitizer} from "@angular/platform-browser";

@Component({
  selector: 'app-bill',
  templateUrl: './bill.component.html',
  styleUrls: ['./bill.component.scss']
})
export class BillComponent extends SubscribeComponent implements OnInit {

  id: number = 0;
  bills: any[] = [];
  url: any;

  constructor(
    private http: HttpClient,
    private sanitizer: DomSanitizer,
  ) {
    super();
  }

  ngOnInit(): void {
    this.get();
  }

  get(): void {
    this.add(this.http.get('api/bills').subscribe((data: any) => {
      this.bills = data['hydra:member'];
      this.setUrl(0);
    }))
  }


  next() {
    if(this.id < this.bills.length - 1 ) {
      this.id ++;
      this.setUrl(this.id);
    }
  }

  previous() {
    if(this.id > 0) {
      this.id --;
      this.setUrl(this.id);
    }
  }

  setUrl(id:any){
    setTimeout(() => {
      this.url =  this.sanitizer.bypassSecurityTrustResourceUrl( 'https://drive.google.com/viewerng/viewer?embedded=true&url=' +this.bills[id].file +'#toolbar=0&scrollbar=0')
    })
  }
}
