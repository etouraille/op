import { Component, OnInit } from '@angular/core';
import {SubscribeComponent} from "../../../lib/component/subscribe/subscribe.component";
import {FormBuilder, FormControl, Validators} from "@angular/forms";
import {HttpClient} from "@angular/common/http";
import {ActivatedRoute, Router} from "@angular/router";
import {of, switchMap, tap} from "rxjs";

@Component({
  selector: 'app-type',
  templateUrl: './type.component.html',
  styleUrls: ['./type.component.scss']
})
export class TypeComponent extends SubscribeComponent implements OnInit {
  form: any = this.fb.group({
    name: ['', Validators.required]
  });
  types: any[] = [];
  _edit = false;
  id: string = '';

  constructor(
    private fb: FormBuilder,
    private http: HttpClient,
    private router: Router,
    private route: ActivatedRoute,
  ) {
    super();
  }

  ngOnInit(): void {
    this.add(this.route.paramMap.pipe(switchMap((param: any) => {
        this.id = param.get('id');
        if(this.id) {
          this._edit = true;
          this.form.addControl('id', new FormControl(this.id));
          return this.http.get('api/thing_types/' + this.id)
        } else {
          this._edit = false;
          return of(false);
        }
      }),
      tap((thing: any) => {
        if(thing) this.form.patchValue(thing);
        this.getTypes();
      })).subscribe());
  }

  submit() {
    if(!this._edit) {
      this.add(this.http.post('api/thing_types', this.form.value).subscribe(
        () => {
          this.getTypes()
          this.form.patchValue({ name: ''});

        }
      ))
    } else {
      this.add(this.http.put('api/thing_types/' + this.id , this.form.value).subscribe(
        () => this.router.navigate(['logged/type-add'])
      ))
    }
  }

  getTypes() {
    this.add(this.http.get('api/thing_types').subscribe((data: any) => {
      this.types = data['hydra:member'];
      if(this.id) {
        console.log('here ======');
        this.types = this.types.filter((type: any) => type.id != parseInt(this.id));
      }
    }))
  }

  edit(id: number, i: number) {
    this.router.navigate(['logged/type/' + id])
  }
}
