import { Component, OnInit } from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {EXTENSION_META_SAVE, EXTENSION_META_TYPE} from "../../../@core/app.interface.data";
import {AppResponseDataOptions} from "../../../@core/app.data.options";
import {ConfigurationService} from "../../../@core/services/configuration.service";
import {ToastService} from "../../../@core/services/toast.service";

@Component({
  selector: 'app-meta',
  templateUrl: './meta.component.html',
  styleUrls: ['./meta.component.scss']
})
export class MetaComponent implements OnInit {

  taxonomies = [];

  selectedMeta: string = "category";

  metas = [];
  submitted: boolean;

  constructor(
    private http: HttpClient,
    private configuration: ConfigurationService,
    private toastService: ToastService
  ) { }

  ngOnInit(): void {
    this.taxonomies = [].concat(
        this.configuration.appConfig.taxonomy,
        this.configuration.appConfig.post_type
      );
  }

  createMeta() {
    const meta = this.metas.find((item) => {
      if (item.taxonomy.value == this.selectedMeta) {
        return item;
      }
      return null;
    });
    if (meta != null) {
      return null;
    }
    let taxonomy = this.taxonomies.find((item)  => {
      if (item.value == this.selectedMeta) {
        return item;
      }
      return null;
    });
    if (taxonomy == null) {
      return null;
    }
    this.http.get(EXTENSION_META_TYPE + '/' + taxonomy.value).subscribe((res:AppResponseDataOptions) => {
      this.metas.push(
        res.data || {
          taxonomy: taxonomy,
          forms: [],
        }
      );
    });
  }

  saveMeta() {
    this.submitted = true;
    this.http.post(EXTENSION_META_SAVE, this.metas).subscribe((res: AppResponseDataOptions) => {
      this.submitted = false;
      this.toastService.showResponseToast(res.code, "元信息", res.message);
    }, (error) => {
      this.submitted = false;
    });
  }
}
