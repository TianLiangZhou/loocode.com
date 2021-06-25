import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {MetaComponent} from "./meta/meta.component";
import {ModelComponent} from "./model/model.component";
import {NewModelComponent} from "./new-model/new-model.component";
import {DynamicModelComponent} from "./dynamic-model/dynamic-model.component";

const routes: Routes = [
  {
    path: "meta",
    component: MetaComponent,
    data: {title: "元信息"}
  },
  {
    path: "model",
    component: ModelComponent,
    data: {title: "模型"}
  },
  {
    path: "new-model",
    component: NewModelComponent,
    data: {title: "新建模型"}
  },
  {
    path: 'editing-model/:id',
    component: NewModelComponent,
    data: {title: '编辑模型'}
  },
  {
    path: "dynamic-model/:id",
    component: DynamicModelComponent,
    data: {title: ""}
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class ExtensionRoutingModule { }
