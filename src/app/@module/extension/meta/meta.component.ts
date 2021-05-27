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

  formType = [
    {'name': '文本', 'type': 'input'},
    {'name': '多行文本', 'type': 'textarea'},
    {'name': '文件', 'type': 'file'},
    {'name': '图像', 'type': 'image'},
    {'name': '选择', 'type': 'select'},
    {'name': '复选', 'type': 'checkbox'},
    {'name': '单选', 'type': 'radio'},
  ];
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

  metaTrash(index: number) {
    this.metas.splice(index, 1);
  }
  metaTrashRow(metaIndex: number, row: number) {
    this.metas[metaIndex].forms.splice(row, 1);
  }

  metaAdd(j: number) {
    this.metas[j].forms.push({
      name: "",
      keyword: "",
      description: "",
      type: "input",
      value: null,
    });
  }

  isMultiple(type: string) {
    return ['select', 'checkbox', 'radio'].includes(type);
  }
  isMedia(type: string) {
    return ['file', 'image'].includes(type);
  }

  metaTrashValueRow(j: number, i: number, k: number) {
    this.metas[j].forms[i].value.splice(k, 1);
  }

  metaValueAdd(j: number, i: number) {
    if (this.metas[j].forms[i].value == null) {
      this.metas[j].forms[i].value = [];
    }
    this.metas[j].forms[i].value.push({
      name: "",
      value: "",
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
