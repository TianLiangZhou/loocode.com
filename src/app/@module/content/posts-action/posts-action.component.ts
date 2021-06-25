import {Component} from '@angular/core';
import {BaseComponent} from "../../../@core/base.component";
import {ActivatedRoute} from "@angular/router";
import {POST_SHOW, POST_STORE, POST_UPDATE} from "../../../@core/app.interface.data";


@Component({
  selector: 'app-posts-action',
  template: `<app-post [update]="update" [store]="store" [show]="show"></app-post>`,
  styles: [],
  // changeDetection: ChangeDetectionStrategy.OnPush,
})
export class PostsActionComponent extends BaseComponent {
  update = POST_UPDATE;
  show = POST_SHOW;
  store = POST_STORE;
}
