import { Component, OnInit } from '@angular/core';
import {FormArray, FormBuilder, FormControl, FormGroup, Validators} from "@angular/forms";
import {SubscribeComponent} from "../../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {Router} from "@angular/router";
import {ToastrService} from "ngx-toastr";

@Component({
  selector: 'app-add',
  templateUrl: './add.component.html',
  styleUrls: ['./add.component.scss']
})
export class AddComponent extends SubscribeComponent implements OnInit {
  addObjectForm = this.fb.group({
    name : ['', Validators.compose([ Validators.required])],
    type: [null, Validators.required],
    description: ['', Validators.compose([ Validators.required])],
    pictures: new FormControl([]),
    price: ['', Validators.compose([ Validators.required])],
    dailyPrice: ['', Validators.compose([ Validators.required])],
    owner: ['', Validators.compose([ Validators.required])],
  });
  users: any[] = [];
  types: any[] = [];


  constructor(
    private fb: FormBuilder,
    private http: HttpClient,
    private toastR: ToastrService,
    private router: Router,
  ) {
    super();
  }

  ngOnInit(): void {

    this.add(
      // @ts-ignore
      this.http.get<any>('api/users?email=&firstname=&lastname=').subscribe(data => {
        this.users = data['hydra:member'];
      })
    )
    this.add(this.http.get('api/thing_types').subscribe((data: any) => {
      this.types = data['hydra:member'];
    }))
  }
  onSubmit(): void {
    let object : any = Object.assign({}, this.addObjectForm.value);
    object.owner = 'api/users/' + object.owner;
    // @ts-ignore
    object.price = parseFloat(object.price);
    object.dailyPrice = parseFloat(object.dailyPrice);
    object.type = 'api/thing_types/' + object.type;
    object.shop = 'api/shops/1';
    object.satus = 'active';
    this.add(
      this.http.post('api/things', object).subscribe(
        data => {
          this.addObjectForm.patchValue({
            name : '',
            description: '',
            price: '',
            pictures: [],
            owner : ''
          })
          this.toastR.success('Objet ajouté');
          this.router.navigate(['logged/thing-list']);
        }
      )
    )
  }

  get pictures() {
    return this.addObjectForm.get('pictures') as FormArray;
  }


}
