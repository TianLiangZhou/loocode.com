import { Component} from '@angular/core';
import {BaseComponent} from "../../../@core/base.component";
import {PAGE_SHOW, PAGE_STORE, PAGE_UPDATE} from "../../../@core/app.interface.data";

@Component({
  selector: 'app-page-new',
  template: `
    <app-post [meta]="false" [store]="store" [update]="update"></app-post>
  `,
  styles: [],
})
export class NewComponent extends BaseComponent {

  store = PAGE_STORE;
  update = PAGE_UPDATE;
  show = PAGE_SHOW;

  init() {

  }
}
