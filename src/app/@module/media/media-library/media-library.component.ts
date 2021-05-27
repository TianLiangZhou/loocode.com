import {AfterViewInit, Component, OnInit} from '@angular/core';
import {CKFinderService} from "../../../@core/services/ckfinder.service";
import {BaseComponent} from "../../../@core/base.component";
import {ActivatedRoute} from "@angular/router";

@Component({
  selector: 'app-media-library',
  templateUrl: './media-library.component.html',
  styleUrls: ['./media-library.component.scss']
})
export class MediaLibraryComponent extends BaseComponent implements AfterViewInit {

  constructor(public ckfinder: CKFinderService, route: ActivatedRoute) {
    super(route);
  }
  init() {

  }

  ngAfterViewInit(): void {
    let build = false;
    let intervalId = setInterval(() => {
      // @ts-ignore
      if (window.hasOwnProperty(["CKFinder"]) && build) {
        clearInterval(intervalId);
        return ;
      }
      window['CKFinder'].widget('ckfinder-widget', {
        width: '100%',
        height: 700,
        language: 'zh-cn',
      });
      build = true;
    }, 500);
  }

}
