import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../lib/component/subscribe/subscribe.component";
import {FormBuilder, FormControl, Validator, Validators} from "@angular/forms";
import {HttpClient} from "@angular/common/http";
import {ToastrService} from "ngx-toastr";
import {Router} from "@angular/router";

@Component({
  selector: 'app-add',
  templateUrl: './add.component.html',
  styleUrls: ['./add.component.scss']
})
export class AddComponent extends SubscribeComponent implements OnInit {

  addForm = this.fb.group({
    name: ['', Validators.required],
    description: [''],
    status: ['pending'],
    pictures: new FormControl([]),
    price: [0, Validators.required]
  })

  constructor(
    private fb: FormBuilder,
    private http: HttpClient,
    private toastR: ToastrService,
    private router: Router,
  ) {
    super();
  }

  ngOnInit(): void {
  }


  submit() {
    let thing : any = this.addForm.value;
    thing.price = parseFloat(thing.price);
    thing.status = 'pending';
    this.add(
      this.http.post('api/thing/add', thing).subscribe((data: any) => {
        this.addForm.patchValue({ name : '', description: '', status: 'pending', pictures: [], price: 0})
        this.toastR.success('Vous venez de proposer un nouvel objet');
        this.router.navigate(['/']);
      }
    ))
  }
}
