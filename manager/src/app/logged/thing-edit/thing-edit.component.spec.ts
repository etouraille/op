import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ThingEditComponent } from './thing-edit.component';

describe('ThingEditComponent', () => {
  let component: ThingEditComponent;
  let fixture: ComponentFixture<ThingEditComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ThingEditComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ThingEditComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
