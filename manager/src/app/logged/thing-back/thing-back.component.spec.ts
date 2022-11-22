import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ThingBackComponent } from './thing-back.component';

describe('ThingBackComponent', () => {
  let component: ThingBackComponent;
  let fixture: ComponentFixture<ThingBackComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ThingBackComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ThingBackComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
