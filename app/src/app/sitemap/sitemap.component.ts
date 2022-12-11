import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";

@Component({
  selector: 'app-sitemap',
  templateUrl: './sitemap.component.html',
  styleUrls: ['./sitemap.component.scss']
})
export class SitemapComponent extends SubscribeComponent implements OnInit {
  sites: any[] = [];

  constructor(
    private http: HttpClient,
  ) {
    super();
  }

  ngOnInit(): void {
    this.add(this.http.get('api/url').subscribe((data: any) => {
      this.sites = data['hydra:member']
    }))
  }

}
