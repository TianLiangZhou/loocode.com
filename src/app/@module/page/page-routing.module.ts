import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {NewComponent} from "./new/new.component";
import {PageComponent} from "./page.component";

const routes: Routes = [
  {
    path: "",
    component: PageComponent,
    data: {title: "所有页面"},
  },
  {
    path: "new",
    component: NewComponent,
    data: {title: "新建页面"},
  },
  {
    path: 'editing/:id',
    component: NewComponent,
    data: {title: "编辑页面"},
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class PageRoutingModule { }
