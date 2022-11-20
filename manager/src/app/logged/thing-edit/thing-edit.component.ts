import { Component, OnInit } from '@angular/core';
import {FormArray, FormBuilder, FormControl, Validators} from "@angular/forms";
import {SubscribeComponent} from "../../../component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {ActivatedRoute, Router} from "@angular/router";
import {switchMap, tap} from "rxjs";

@Component({
  selector: 'app-thing-edit',
  templateUrl: './thing-edit.component.html',
  styleUrls: ['./thing-edit.component.scss']
})
export class ThingEditComponent extends SubscribeComponent implements OnInit {
  editThingForm: any = this.fb.group({
    id: [''],
    name : ['', Validators.compose([ Validators.required])],
    description: ['', Validators.compose([ Validators.required])],
    pictures: new FormControl([]),
    price: ['', Validators.compose([ Validators.required])],
    owner: ['', Validators.compose([ Validators.required])],
  });
  users: any[] = [];
  thing: any;
  pictures: any = { control: []};
  id: any;


  constructor(
    private http: HttpClient,
    private route: ActivatedRoute,
    private fb: FormBuilder
  ) {
    super()
  }

  ngOnInit(): void {
    this.add(this.route.paramMap.pipe(
      switchMap((param: any) => {
        this.id = param.get('id');
      return  this.http.get('api/things/' + this.id);
    }), tap((data: any) => {
        data.owner = data.owner.id;
        this.thing = data;
        this.editThingForm.patchValue(this.thing);
        })
    ).subscribe())
    this.add(
      this
        .http
        .get('api/users')
        .subscribe((data: any) => {
          this.users = data['hydra:member'];
        })
    )
  }

  submit(): void {
    let obj = this.editThingForm.value;
    obj.owner = 'api/users/' + obj.owner;
    this.add(this.http.put('api/things/' + this.id, obj).subscribe())
  }

  addPicture() {

  }

}
