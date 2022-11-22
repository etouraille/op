import { Component, OnInit } from '@angular/core';
import {FormArray, FormBuilder, FormControl, FormGroup, Validators} from "@angular/forms";
import {SubscribeComponent} from "../../../lib/component/subscribe/subscribe.component";
import {HttpClient} from "@angular/common/http";
import {Router} from "@angular/router";

@Component({
  selector: 'app-add',
  templateUrl: './add.component.html',
  styleUrls: ['./add.component.scss']
})
export class AddComponent extends SubscribeComponent implements OnInit {
  addObjectForm = this.fb.group({
    name : ['', Validators.compose([ Validators.required])],
    description: ['', Validators.compose([ Validators.required])],
    pictures: new FormArray([]),
    price: ['', Validators.compose([ Validators.required])],
    owner: ['', Validators.compose([ Validators.required])],
  });
  users: any[] = [];


  constructor(
    private fb: FormBuilder,
    private http: HttpClient,
  ) {
    super();
  }

  ngOnInit(): void {

    this.addPicture();
    this.add(
      // @ts-ignore
      this.http.get<any>('api/users?email=&firstname=&lastname=').subscribe(data => {
        this.users = data['hydra:member'];
      })
    )
  }
  onSubmit(): void {
    let object = Object.assign({}, this.addObjectForm.value);
    object.owner = 'api/users/' + object.owner;
    // @ts-ignore
    object.price = parseFloat(object.price);
    this.add(
      this.http.post('api/things', object).subscribe(
        data => {
          this.addObjectForm.patchValue({name : '', description: '', price: '', pictures: [],owner : '' })
          this.addPicture();
        }
      )
    )
  }

  get pictures() {
    return this.addObjectForm.get('pictures') as FormArray;
  }

  addPicture() {
    this.pictures.push(this.fb.group({ picture : ['']}));
  }
}
