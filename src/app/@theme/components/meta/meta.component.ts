import {Component, Input, OnChanges, OnInit, SimpleChanges} from "@angular/core";
import {CKFinderService} from "../../../@core/services/ckfinder.service";

@Component({
  selector: "meta-container",
  templateUrl: "meta.component.html",
  styleUrls: [],
})
export class MetaComponent implements OnInit, OnChanges {

  @Input() metas = [];

  metaBindModel = {};

  constructor(public ckfinder: CKFinderService) {

  }

  ngOnChanges(changes: SimpleChanges): void {
  }

    ngOnInit() {
      this.ckfinder.subscribe((files: any[]) => {
        console.log(files);
        if (this.metas.length > 0) {
          this.metas.forEach((item) => {
            if (!['file', 'image'].includes(item.type)) {
              return ;
            }
            if (item.value) {
              this.metaBindModel[item.keyword] = [];
            } else {
              this.metaBindModel[item.keyword] = null;
            }
            files.forEach((file) => {
              if (item.keyword == file.origin) {
                if (item.value) {
                  this.metaBindModel[item.keyword].push(file.url);
                } else {
                  this.metaBindModel[item.keyword] = file.url;
                }
              }
            });
          });
          console.log(this.metaBindModel);
        }
      });
    }

}
