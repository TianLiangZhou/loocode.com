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

  @Input() value = {};

  constructor(public ckfinder: CKFinderService) {

  }

  ngOnChanges(changes: SimpleChanges): void {
    if (changes?.value) {
      if (Object.keys(changes.value.currentValue).length === 0) {
        this.metaBindModel = {};
      } else {
        for (let key in changes.value.currentValue) {
          if (changes.value.currentValue.hasOwnProperty(key)) {
            let value = changes.value.currentValue[key];
            this.metaBindModel[key] = typeof value == "string" && (value.indexOf("[") == 0 || value.indexOf("[") == 0)
              ? JSON.parse(value)
              : value;
          }
        }
      }
    }
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
