import { Component, OnInit } from '@angular/core';
import {FormArray, FormBuilder, FormControl, Validators} from "@angular/forms";
import {SubscribeComponent} from "../../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {ActivatedRoute, Router} from "@angular/router";
import {switchMap, tap} from "rxjs";
import {ToastrService} from "ngx-toastr";

@Component({
  selector: 'app-thing-edit',
  templateUrl: './thing-edit.component.html',
  styleUrls: ['./thing-edit.component.scss']
})
export class ThingEditComponent extends SubscribeComponent implements OnInit {
  editThingForm: any = this.fb.group({
    id: [''],
    name : ['', Validators.compose([ Validators.required])],
    type: ['', Validators.required],
    description: ['', Validators.compose([ Validators.required])],
    pictures: new FormControl([]),
    price: ['', Validators.compose([ Validators.required])],
    dailyPrice: ['', Validators.compose([ Validators.required])],
    owner: ['', Validators.compose([ Validators.required])],
  });
  users: any[] = [];
  thing: any;
  pictures: any = { control: []};
  id: any;
  types: any[] = [];


  constructor(
    private http: HttpClient,
    private route: ActivatedRoute,
    private fb: FormBuilder,
    private router: Router,
    private toastR: ToastrService,
  ) {
    super()
  }

  ngOnInit(): void {
    this.add(this.route.paramMap.pipe(
      switchMap((param: any) => {
        this.id = param.get('id');
      return  this.http.get('api/things/' + this.id);
    }), tap((data: any) => {
        data.owner = data?.owner?.id;
        this.thing = data;
        this.editThingForm.patchValue(this.thing);
        })
    ).subscribe())
    this.add(
      this
        .http
        .get('api/users?email=&lastname=&firstname=')
        .subscribe((data: any) => {
          this.users = data['hydra:member'];
        })
    )
    this.add(this.http.get('api/thing_types').subscribe((data: any) => {
      this.types = data['hydra:member'];
    }))
  }

  submit(): void {
    let obj : any = Object.assign({}, this.editThingForm.value);
    obj.owner = 'api/users/' + obj.owner;
    obj.dailyPrice = parseFloat(obj.dailyPrice);
    obj.price = parseFloat(obj.price)
    this.add(this.http.put('api/things/' + this.id, obj).subscribe(() => {
      this.toastR.success('Objet modifié');
      this.router.navigate(['logged/thing-list'])
    }, (error: any) => {
      this.toastR.error(error, 'erreur');
    }))
  }

  addPicture() {

  }

}
