import { Component, OnInit } from '@angular/core';
import {FormBuilder, Validators} from "@angular/forms";

@Component({
  selector: 'app-add',
  templateUrl: './add.component.html',
  styleUrls: ['./add.component.scss']
})
export class AddComponent implements OnInit {
  addObjectForm = this.fb.group({
    name : ['', Validators.compose([ Validators.required])],
    description: ['', Validators.compose([ Validators.required])],
    picture: [''],
    price: ['', Validators.compose([ Validators.required])],
    owner: ['', Validators.compose([ Validators.required])],
  });


  constructor(private fb: FormBuilder) { }

  ngOnInit(): void {
  }
  onSubmit(): void {

  }
}
