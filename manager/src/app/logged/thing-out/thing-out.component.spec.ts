import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ThingOutComponent } from './thing-out.component';

describe('ThingOutComponent', () => {
  let component: ThingOutComponent;
  let fixture: ComponentFixture<ThingOutComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ThingOutComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ThingOutComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
