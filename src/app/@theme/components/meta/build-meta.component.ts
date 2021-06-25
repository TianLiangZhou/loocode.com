import {Component, Input, OnChanges, OnInit, SimpleChanges} from "@angular/core";

@Component({
  selector: "build-meta-container",
  templateUrl: "build-meta.component.html",
  styles: [`
    table td {
      vertical-align: middle;
    }
    table caption {
      caption-side: inherit;
    }

    table th, table td {
      border: none;
    }`
  ]
})
export class BuildMetaComponent implements OnInit, OnChanges {

  formType = [
    {'name': '文本', 'type': 'input'},
    {'name': '多行文本', 'type': 'textarea'},
    {'name': '文件', 'type': 'file'},
    {'name': '图像', 'type': 'image'},
    {'name': '选择', 'type': 'select'},
    {'name': '复选', 'type': 'checkbox'},
    {'name': '单选', 'type': 'radio'},
    {'name': '日期', 'type': 'date'},
    {'name': '时间', 'type': 'datetime'},
    {'name': '范围日期', 'type': 'range_date'},
    {'name': '自动完成', 'type': 'input_auto'}
  ];

  @Input() meta: {[key: string]: any} = [];

  metaBindModel = {};

  ngOnChanges(changes: SimpleChanges): void {
  }

  ngOnInit() {

  }
  metaTrash(index: number) {
    this.meta.splice(index, 1);
  }
  metaTrashRow(row: number) {
    this.meta.forms.splice(row, 1);
  }

  metaAdd() {
    this.meta.forms.push({
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

  metaTrashValueRow(i: number, k: number) {
    this.meta.forms[i].value.splice(k, 1);
  }

  metaValueAdd(i: number) {
    if (this.meta.forms[i].value == null) {
      this.meta.forms[i].value = [];
    }
    this.meta.forms[i].value.push({
      name: "",
      value: "",
    });
  }
}
